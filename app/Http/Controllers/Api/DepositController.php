<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of deposits (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $perPage = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = Deposit::with(['user:id,name,email,avatar', 'processedBy:id,name'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ]);
        }

        $deposits = $query->paginate($perPage, ['*'], 'page', $page);

        // Get statistics
        $statistics = [
            'pending_count' => Deposit::pending()->count(),
            'approved_count' => Deposit::approved()->count(),
            'rejected_count' => Deposit::rejected()->count(),
            'today_total' => Deposit::whereDate('created_at', today())->sum('amount'),
            'today_approved' => Deposit::approved()->whereDate('created_at', today())->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'deposits' => $deposits->items(),
                'pagination' => [
                    'current_page' => $deposits->currentPage(),
                    'per_page' => $deposits->perPage(),
                    'total' => $deposits->total(),
                    'total_pages' => $deposits->lastPage(),
                    'has_next' => $deposits->hasMorePages(),
                    'has_prev' => $deposits->currentPage() > 1,
                ],
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Store a newly created deposit request
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10000|max:100000000',
            'description' => 'nullable|string|max:500',
            'bank_account' => 'required|string|max:50',
            'proof_image' => 'required|string', // base64 or file path
        ], [
            'amount.required' => 'Số tiền là bắt buộc',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền tối thiểu là 10,000 VNĐ',
            'amount.max' => 'Số tiền tối đa là 100,000,000 VNĐ',
            'bank_account.required' => 'Số tài khoản ngân hàng là bắt buộc',
            'proof_image.required' => 'Hình ảnh chứng minh là bắt buộc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Handle proof image upload
            $proofImagePath = $this->handleImageUpload($request->proof_image);

            // Generate reference code
            $referenceCode = Deposit::generateReferenceCode($user->id);

            // Create deposit
            $deposit = Deposit::create([
                'reference_code' => $referenceCode,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'description' => $request->description,
                'bank_account' => $request->bank_account,
                'proof_image' => $proofImagePath,
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu nạp tiền đã được tạo',
                'data' => [
                    'id' => $deposit->reference_code,
                    'user_id' => $deposit->user_id,
                    'amount' => $deposit->amount,
                    'description' => $deposit->description,
                    'status' => $deposit->status,
                    'bank_account' => $deposit->bank_account,
                    'proof_image' => $deposit->proof_image,
                    'reference_code' => $deposit->reference_code,
                    'created_at' => $deposit->created_at,
                    'updated_at' => $deposit->updated_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo yêu cầu nạp tiền',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's deposit history
     */
    public function userDeposits(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $deposits = $user->deposits()
            ->select(['id', 'reference_code', 'amount', 'description', 'status', 'admin_note', 'created_at', 'processed_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'deposits' => $deposits->items(),
                'pagination' => [
                    'current_page' => $deposits->currentPage(),
                    'per_page' => $deposits->perPage(),
                    'total' => $deposits->total(),
                    'total_pages' => $deposits->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Update deposit status (Admin only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Trạng thái là bắt buộc',
            'status.in' => 'Trạng thái phải là approved hoặc rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deposit = Deposit::where('reference_code', $id)->first();

            if (!$deposit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu nạp tiền'
                ], 404);
            }

            if ($deposit->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu này đã được xử lý'
                ], 400);
            }

            DB::beginTransaction();

            $admin = Auth::user();
            
            if ($request->status === 'approved') {
                $deposit->approve($admin->id, $request->admin_note);
            } else {
                $deposit->reject($admin->id, $request->admin_note);
            }

            DB::commit();

            $deposit->load('processedBy:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => [
                    'id' => $deposit->reference_code,
                    'status' => $deposit->status,
                    'admin_note' => $deposit->admin_note,
                    'processed_by' => [
                        'id' => $deposit->processedBy->id,
                        'name' => $deposit->processedBy->name,
                    ],
                    'processed_at' => $deposit->processed_at,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle image upload (base64 or file)
     */
    private function handleImageUpload($imageData): string
    {
        // Check if it's base64 encoded image
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('Invalid image type');
            }
            
            $imageData = base64_decode($imageData);
            
            if ($imageData === false) {
                throw new \Exception('Base64 decode failed');
            }
            
            $fileName = 'deposits/proof_' . time() . '_' . uniqid() . '.' . $type;
            Storage::disk('public')->put($fileName, $imageData);
            
            return Storage::disk('public')->url($fileName);
        }
        
        // If it's already a URL or file path, return as is
        return $imageData;
    }
}
