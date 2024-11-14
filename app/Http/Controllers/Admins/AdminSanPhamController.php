<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSanPhamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy ra toàn bộ dữ liệu
        $listSanPham = DB::table('san_phams')->orderByDesc('id')->paginate(5);

        // Kết quả trả ra là một mảng các đối tượng
        // dd($listSanPham);

        return view('admins.sanphams.index', compact('listSanPham'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.sanphams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
            // Xử lý hình ảnh
            $filePath = null;
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = uniqid() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/sanpham', $fileName, 'public');
            }

            // Xử lý thêm dữ liệu
            $dataSanPham = [
                'ma_san_pham' => $request->input('ma_san_pham'),
                'ten_san_pham' => $request->input('ten_san_pham'),
                'gia' => $request->input('gia'),
                'gia_khuyen_mai' => $request->input('gia_khuyen_mai'),
                'so_luong' => $request->input('so_luong'),
                'ngay_nhap' => $request->input('ngay_nhap'),
                'mo_ta' => $request->input('mo_ta'),
                'hinh_anh' => $filePath,
                'trang_thai' => $request->input('trang_thai'),
                'created_at' => now(),
                'updated_at' => null,
            ];
            // Kiểm tra xem đã lấy được đủ dữ liệu lên chưa
            // dd($dataSanPham);

            // Lưu dữ liệu vào database
            DB::table('san_phams')->insert($dataSanPham);

            DB::commit();

            // Chuyển hướng về trang danh sách và hiển thị thông báo
            return redirect()->route('sanphams.index')
                                ->with('success', 'Thêm sản phẩm thành công!');

        } catch (\PDOException $e) {
            DB::rollBack();
            
            return redirect()->route('sanphams.index')
                                ->with('error', 'Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại sau!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
