# Đề tài: Xây dựng hệ thống phân tích năng lực học tập của sinh viên dựa trên kết quả học tập  

**Sinh viên thực hiện:**
  - Họ tên: Nguyễn Thị Ngọc Chăm
  - MSSV: 110120008
  - Lớp: DA20TTA
  - Email: ngoccham0912@gmail.com

**Chức năng hệ thống:**
  - Quản lý thông tin chung về chương trình đào tạo, môn học, sinh viên, điểm tích lũy của sinh viên
  - Phân tích điểm mạnh, điểm yếu của sinh viên dựa trên điểm số sinh viên đã tích lũy
  - Gợi ý cải thiện môn học phù hợp với năng lực học tập của từng sinh viên

## Cài đặt
- Cài đặt [PHP](https://www.php.net/downloads.php) (phiên bản từ 8.1 trở lên)
- Cài đặt [Xampp](https://www.apachefriends.org/download.html)
- Cài đặt [Python](https://www.python.org/downloads/)
- Cài đặt [Composer](https://getcomposer.org/)
- Tải dự án về máy: `git clone https://github.com/Ngoccham05/tn-da20tta-110120008-nguyenthingoccham-danhgiadangluchoctap.git`
- Sao chép tệp _.env.example_ thành _.env_
- Tạo khóa ứng dụng trong _.env_: `php artisan key:generate`
- Thay đổi cài đặt trong tệp _.env_:
  - DB_CONNECTION
  - DB_DATABASE
  - DB_USERNAME
  - DB_PASSWORD
- Import dữ liệu từ _db_danhgianangluc_ trong thư mục _public/db_ (có dữ liệu mẫu)
