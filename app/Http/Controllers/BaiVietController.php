<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Http\Resources\BaiVietResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBaiVietRequest;
use App\Http\Requests\UpdateBaiVietRequest;

class BaiVietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy ra danh sách toàn bộ bài viết
        // Sử dụng Eloquent để truy xuất dữ liệu
        // Để sử dụng đc eloquent bắt buộc phải có model
        $baiViets = BaiViet::orderByDesc('id')->paginate(5);

        // dd($baiViets);

        // Trả về thông tin bài viết dưới dạng JSON
        return BaiVietResource::collection($baiViets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBaiVietRequest $request)
    {
        // Xử lý hình ảnh
        $filePath = null;
        if ($request->hasFile('hinh_anh')) {
            $filePath = $request->file('hinh_anh')->store('uploads/baiviet', 'public');
        }

        // Xử lý thêm dữ liệu
        $dataBaiViet = [
            'hinh_anh' => $filePath,
            'tieu_de' => $request->input('tieu_de'),
            'noi_dung' => $request->input('noi_dung'),
            'ngay_dang' => $request->input('ngay_dang'),
            'trang_thai' => $request->input('trang_thai')
        ];

        // Lưu dữ liệu vào database
        $newBaiViet = BaiViet::create($dataBaiViet);

        // Trả ra dữ liệu bài viết dưới dạng json
        return response()->json([
            'message'   => 'Thêm bài viết thành công',
            'data'      => new BaiVietResource($newBaiViet)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BaiViet $baiviet)
    {
        if ($baiviet) {
            return new BaiVietResource($baiviet);
        } else {
            return response()->json([
                'message' => 'Bài viết không tồn tại'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBaiVietRequest $request, BaiViet $baiviet)
    {
        // Kiểm tra bài viết đó có tồn tại hay không
        if (!$baiviet) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm']);
        }
        // Xử lý hình ảnh
        $filePath = $baiviet->hinh_anh; // Giữ nguyên hình ảnh cũ nếu có
        if ($request->hasFile('hinh_anh')) {
            $filePath = $request->file('hinh_anh')->store('uploads/baiviet', 'public');
            // Xóa hình cũ nếu có hình ảnh mới đẩy lên
            if ($baiviet->hinh_anh && Storage::disk('public')->exists($baiviet->hinh_anh)) {
                Storage::disk('public')->delete($baiviet->hinh_anh);
            }
        }

        // Xử lý cập nhật dữ liệu
        $dataBaiViet = [
            'hinh_anh' => $filePath,
            'tieu_de' => $request->input('tieu_de'),
            'noi_dung' => $request->input('noi_dung'),
            'ngay_dang' => $request->input('ngay_dang'),
            'trang_thai' => $request->input('trang_thai')
        ];
        // dd($dataBaiViet);
        $baiviet->update($dataBaiViet);

        // Trả ra dữ liệu bài viết dưới dạng json
        return response()->json([
            'message'   => 'Sửa bài viết thành công',
            'data'      => new BaiVietResource($baiviet)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BaiViet $baiviet)
    {
        // Kiểm tra bài viết đó có tồn tại hay không
        if (!$baiviet) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm']);
        }
        // Lưu trữ đường dẫn của hình ảnh vào đây
        $filePath = $baiviet->hinh_anh;
        $deleteBaiViet = $baiviet->delete();
        // Nếu xóa thành công thì tiến hành xóa ảnh
        if ($deleteBaiViet) {
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return response()->json(['message' => 'Xóa bài viết thành công!']);
        }
        return response()->json(['message' => 'Xóa bài viết thất bại!']);
    }
}
