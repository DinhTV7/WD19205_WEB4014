<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiViet extends Model
{
    use HasFactory;

    /**
     * Quy định bảng mà model sẽ mapping tới
     */
    protected $table = 'bai_viets';

    /**
     * Tất cả các trường dữ liệu muốn thao tác cần phải đưa vào fillable
     */
    protected $fillable = [
        'hinh_anh',
        'tieu_de',
        'noi_dung',
        'ngay_dang',
        'trang_thai'
    ];

    // Tắt chế độ tự động cập nhật created_at và updated_at
    public $timestamps = false;
}
