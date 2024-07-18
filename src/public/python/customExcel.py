import os
import pandas as pd
import numpy as np
import datetime
from flask import Flask, request, jsonify
from flask_cors import CORS
from sklearn.neighbors import KNeighborsClassifier
from sklearn.model_selection import cross_val_score
from sklearn.model_selection import KFold, cross_val_score
from sklearn.metrics import accuracy_score

app = Flask(__name__)
CORS(app)

def trung_binh_nhom(X, nhom_mon_hoc):
  X = np.array(X, dtype=float) 
  diem_trung_binh = np.zeros_like(X)
  start = 0

  for so_mon in nhom_mon_hoc.values():
    end = start + so_mon
    diem_trung_binh[:, start:end] = X[:, start:end].mean(axis=1).reshape(-1, 1)
    start = end
  return diem_trung_binh

def gan_nhan_diem(diem_mon, trung_binh_nhom):
  return 1 if diem_mon <= 1.5 or diem_mon + 0.75 < trung_binh_nhom else 0

def cross_validate_knn(X, nhom_mon_hoc):
  kf = KFold(n_splits=5, shuffle=True, random_state=42)
  accuracies = []

  diem_trung_binh_nhom = trung_binh_nhom(X, nhom_mon_hoc)
  y = np.zeros_like(X)
  
  for i in range(X.shape[0]):
    for j in range(X.shape[1]): 
      y[i, j] = gan_nhan_diem(X[i, j], diem_trung_binh_nhom[i, j]) 

  for train_index, test_index in kf.split(X):
    X_train, X_test = X[train_index], X[test_index]
    y_train, y_test = y[train_index], y[test_index]

    knn = KNeighborsClassifier(n_neighbors=7)
    knn.fit(X_train, y_train)

    y_pred = knn.predict(X_test)

    # print(X_train)
    # print("--------------------")
    # print(y_train)

    accuracy = accuracy_score(y_test.flatten(), y_pred.flatten())
    accuracies.append(accuracy)

  return np.mean(accuracies), np.std(accuracies)


def goiy_mon_can_cai_thien(X, nhom_mon_hoc, index_sv):
  diem_trung_binh_nhom = trung_binh_nhom(X, nhom_mon_hoc)
  # gán nhãn 
  y = np.vectorize(gan_nhan_diem)(X, diem_trung_binh_nhom)
  # print(y)

  X_train = np.delete(X, index_sv, axis=0)
  y_train = np.delete(y, index_sv, axis=0)
  X_test = X[index_sv].reshape(1, -1)

  knn = KNeighborsClassifier(n_neighbors=7)
  knn.fit(X_train, y_train)
  neighbors = knn.kneighbors(X_test, return_distance=False)

  predicted_neighbors = y_train[neighbors].reshape(-1, y_train.shape[1])

  count_1 = np.sum(predicted_neighbors == 1, axis=0)
  count_0 = np.sum(predicted_neighbors == 0, axis=0)

  mon_can_cai_thien = [i + 1 for i in range(X.shape[1]) if y[index_sv, i] == 1 and count_1[i] < count_0[i]]

  return mon_can_cai_thien


def tao_mang(diem):
  return np.array([[float(value) for value in row] for row in diem])

@app.route('/goiycaithien', methods=['POST'])
def goiycaithien():
  data = request.get_json()

  diem = tao_mang(data['diem'])
  nhom_mon_hoc = {int(k): v for k, v in data['nhom_mon'].items()}
  index_sv = data['vi_tri'] -1
  # print(index_sv)

  mon_can_cai_thien = goiy_mon_can_cai_thien(diem, nhom_mon_hoc, index_sv)

  mean_accuracy, std_accuracy = cross_validate_knn(diem, nhom_mon_hoc)
  print(mean_accuracy, std_accuracy)

  return jsonify(mon_can_cai_thien)








#---------------Custom Excel-------------------------------------------------
@app.route('/upload', methods=['POST'])
def upload_file():
  if 'file' not in request.files:
    return jsonify({'error': 'No file part'}), 400

  file = request.files['file']
  if file.filename == '':
    return jsonify({'error': 'No selected file'}), 400
  
  # thư mục đầu vào - ra
  input_dir = 'public/xulydiem/input'
  output_dir = 'public/xulydiem/output'

  if not os.path.exists(input_dir):
    os.makedirs(input_dir)
  if not os.path.exists(output_dir):
    os.makedirs(output_dir)

  # tên file xuất
  current_date = datetime.datetime.now().strftime("%d%m%Y")
  base_filename, file_extension = os.path.splitext(file.filename)
  input_filename = f"{base_filename}_{current_date}{file_extension}"
  output_filename = f"{base_filename}_tb_{current_date}.xlsx"
  output_filename_mon = f"{base_filename}_mon_{current_date}.xlsx"
  output_filename_tb = f"{base_filename}_tb_{current_date}.xlsx"

  # lưu file input
  file_path = os.path.join(input_dir, input_filename)
  file.save(file_path)
  
  # file output
  output_1 = os.path.join(output_dir, output_filename)
  output_2 = os.path.join(output_dir, output_filename_mon)
  output_3 = os.path.join(output_dir, output_filename_tb)

  # Xử lý file Excel
  loclan1(file_path, output_1)
  loclan2(output_1, output_2)
  loclan3(output_1, output_3)

  return jsonify({'message': 'File uploaded and processed successfully', 'output_file_mon': output_2, 'output_file_hk': output_3}), 200

def loclan1(input_file_path, output_file_path):
  df = pd.read_excel(input_file_path)
  
  start = df[df.iloc[:, 0] == 'Sinh viên'].index.tolist()
  end = df[df.iloc[:, 0] == 'NGƯỜI LẬP BIỂU'].index.tolist()

  if len(start) == 0 or len(end) == 0:
    print("Không tìm thấy")
  else:
    with pd.ExcelWriter(output_file_path, engine='openpyxl') as writer:
      num_row = 1
      for i in range(min(len(start), len(end))):
        start_index = start[i]
        end_index = end[i]

        # Chia 2 phần theo cột T
        phan1 = df.iloc[start_index:end_index, :19]
        phan2 = df.iloc[start_index:end_index, 19:]

        # Lọc theo từ khóa
        loc_phan_1 = phan1[phan1.iloc[:, 0].str.contains(r'^\d+$|Năm học|Học kỳ|ĐTBHK', na=False, regex=True)]
        loc_phan_2 = phan2[phan2.iloc[:, 0].str.contains(r'^\d+$|Năm học|Học kỳ|ĐTBHK', na=False, regex=True)]

        # In cột
        loc_phan_1 = loc_phan_1.iloc[:, [0, 2, 4, 14, 15 ,16]]
        loc_phan_2 = loc_phan_2.iloc[:, [0, 1, 3, 10, 12 ,13]]

        # Lấy mã SV
        ma_sv = phan1.loc[phan1.iloc[:, 0] == 'Mã SV', phan1.columns[6]].values[0]

        # Thêm cột mới
        loc_phan_1.insert(0, 'Mã SV', ma_sv)
        loc_phan_2.insert(0, 'Mã SV', ma_sv)

        # Xuất Excel
        loc_phan_1.to_excel(writer, sheet_name='sheet1',startrow= num_row, index=False, header=False)
        num_row = num_row + len(loc_phan_1)
        loc_phan_2.to_excel(writer, sheet_name='sheet1',startrow= num_row, index=False, header=False)
        num_row = num_row + len(loc_phan_2)

      # Đặt tên cột
      column_names = ['ma_sinh_vien', 'tt', 'ma_mon_hoc', 'ten_mon_hoc', 'diem_lan_1', 'diem_lan_2', 'diem_he_4']
      workbook = writer.book
      worksheet = writer.sheets['sheet1']
      for i, col_name in enumerate(column_names):
        worksheet.cell(row=1, column=i+1, value=col_name)

def loclan2(file_path, output_file_path):
  data1 = pd.read_excel(file_path)
  
  # lọc lấy học kỳ - năm học
  nh = data1[data1.iloc[:, 1].astype(str).str.contains('Năm học', case=False)].index.tolist()
  hk = data1[data1.iloc[:, 1].astype(str).str.contains('Học kỳ', case=False)].index.tolist()
  data = data1[data1.iloc[:, 1].str.contains(r'^\d+$', na=False, regex=True)].index.tolist()

  nh.append(float('inf'))
  hk.append(float('inf'))

  new_data = pd.DataFrame(columns=["hk_nk", "ma_sinh_vien", "ma_mon_hoc", "diem_lan_1", "diem_lan_2", "diem_he_4"])

  for i, i_next in zip(nh, nh[1:]):
    for j, j_next in zip(hk, hk[1:]):
      if i < j < i_next:
        hk_nh = format_hknh(data1.iloc[j, 1] + ", " + data1.iloc[i, 1])
        for d in data:
          if j < d < j_next:
            row_data = data1.iloc[d, [0, 2, 4, 5, 6]].tolist()
            row_data[1] = format_string(str(row_data[1]))
            row_data.insert(0, hk_nh)
            new_data.loc[len(new_data)] = row_data

  new_data.to_excel(output_file_path, index=False)

def loclan3(file_path, output_file_path):
  data1 = pd.read_excel(file_path)
  
  nh = data1[data1.iloc[:, 1].astype(str).str.contains('Năm học', case=False)].index.tolist()
  hk = data1[data1.iloc[:, 1].astype(str).str.contains('Học kỳ', case=False)].index.tolist()
  data = data1[data1.iloc[:, 1].astype(str).str.contains('ĐTBHK', case=False)].index.tolist()

  nh.append(float('inf'))
  hk.append(float('inf'))
  data.append(float('inf'))

  new_data = pd.DataFrame(columns=["hknh", "masv", "diem", "tbhocky", "tbtichluy"])

  for i, i_next in zip(nh, nh[1:]):
    for j, j_next in zip(hk, hk[1:]):
      if i < j < i_next:
        hk_nh = format_hknh(data1.iloc[j, 1] + ", " + data1.iloc[i, 1])
        for d in data:
          if j < d < j_next:
            row_data = data1.iloc[d, [0, 1]].tolist()
            tbhk, tbtl = format_diem(str(row_data[1]))
            
            row_data.insert(3, tbhk)
            row_data.insert(4, tbtl)
            row_data.insert(0, hk_nh)
            new_data.loc[len(new_data)] = row_data

  new_data.to_excel(output_file_path, index=False)

# xử lý mã môn
def format_string(s):
  if len(s) < 6:
    s = s.ljust(6, '0')
  elif len(s) > 6:
    s = s[0:6]
  return s

# xử lý điểm trung bình
def format_diem(chuoi):
  phan_tach = chuoi.split()
    
  try:
    tbhk = float(phan_tach[1])
    tbtl = float(phan_tach[3])
    return tbhk, tbtl
  
  except ValueError:
    return 0, 0

# xử lý mã học kỳ, niên khóa
def format_hknh(hknh):
  parts = hknh.split(", Năm học ")
  if len(parts) != 2:
    return hknh

  hk_part = parts[0].split(" ")[-1]
  year_parts = parts[1].split("-")
  if len(year_parts) != 2:
    return hknh

  year_part1 = year_parts[0][-3:-1]
  year_part2 = year_parts[1][-2:]

  formatted = hk_part + year_part1 + year_part2
  return formatted

if __name__ == '__main__':
  app.run(debug=True, host='0.0.0.0', port=5000)