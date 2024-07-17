        <!-- Content -->

        </div> <!-- end container-fluid -->
      </div> <!-- end content -->

      <!-- Footer Start -->
      <footer class="footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              © 2024 - Nguyễn Thị Ngọc Chăm
            </div>
          </div>
        </div>
      </footer><!-- end Footer -->
    </div>
  </div><!-- END wrapper -->

  <script>
    $(document).ready(function() {
      // kiểm tra và báo lỗi file upload - môn học - sinh viên - giảng viên
      $('#nav-default-tab').on('click', function(){
        $('#btnThem').removeClass('d-none');
        $('#btnThemFile').addClass('d-none');
      })

      $('#nav-file-tab').on('click', function(){
        $('#btnThem').addClass('d-none');
        $('#btnThemFile').removeClass('d-none');
        $('.dropify-wrapper').siblings('.error-text').remove();
      })

      $('#fileUpload').on('change', function() {
        $('.dropify-wrapper').siblings('.error-text').remove();
      })
    });

    function formatDateFromString(dateString) {
      var dateParts = dateString.split("-");
      return dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
    }

    function formatDateFromTimestamp(timestamp) {
      let date = new Date(timestamp * 1000);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      let year = date.getFullYear();
      return [day, month, year];
    }

    function customThongBao(){
      toastr.options = {
        closeButton: true,
        positionClass: "toast-top-right",
        showDuration: "150",
        hideDuration: "150",
        timeOut: "2000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
      };
    }

    function customThongBaoCho(){
      toastr.options = {
        closeButton: true,
        positionClass: "toast-top-right",
        showDuration: "150",
        hideDuration: "150",
        timeOut: 0,
        extendedTimeOut: 0,
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
      };
    }

    function capNhatStt(tt){
      var table = $('#datatable').DataTable();

      var pageInfo = table.page.info();
      var currentPageLength = pageInfo.length;

      var rowId = '#row_' + tt;
      var rowIndex = table.row(rowId).index();

      if (rowIndex !== -1) {
        table.row(rowId).remove().draw(false);

        table.rows().every(function (idx) {
          var currentPageIdx = currentPageLength + idx + 1 - 10;
          var row = this.node();
          $(row).find('#stt').text(currentPageIdx);                      
          // $(row).attr('id', 'row_' + currentPageIdx);
        });
      } else {
        console.error('Không tìm thấy dòng cần xóa');
      }
    }

    function capNhatSttSelectionTable(tt){
      var table = $('#selection-datatable').DataTable();

      var pageInfo = table.page.info();
      var currentPageLength = pageInfo.length;

      var rowId = '#row_' + tt;
      var rowIndex = table.row(rowId).index();

      if (rowIndex !== -1) {
        table.row(rowId).remove().draw(false);

        table.rows().every(function (idx) {
          var currentPageIdx = currentPageLength + idx + 1 - 10;
          var row = this.node();
          $(row).find('#stt').text(currentPageIdx);                      
          // $(row).attr('id', 'row_' + currentPageIdx);
        });
      } else {
        console.error('Không tìm thấy dòng cần xóa');
      }
    }

    function customFormThem(){
      $('.modal-title').html('Thêm');
      $('#xoaForm').addClass('d-none');
      $('#themSuaForm').removeClass('d-none');
      $('#btnThem').removeClass('d-none');
      $('#btnThem').prop('disabled', false);
      $('#btnSua').addClass('d-none');    
      $('#btnXoa').addClass('d-none');
    }

    function customFormSua(){
      $('.modal-title').html('Cập nhật');
      $('#xoaForm').addClass('d-none');
      $('#themSuaForm').removeClass('d-none');
      $('#btnThem').addClass('d-none');
      $('#btnSua').removeClass('d-none');
      $('#btnSua').prop('disabled', false);    
      $('#btnXoa').addClass('d-none');
    }

    function customFormXoa(){
      $('.modal-title').html('Xóa');
      $('#xoaForm').removeClass('d-none');
      $('#themSuaForm').addClass('d-none');
      $('#btnThem').addClass('d-none');
      $('#btnSua').addClass('d-none');    
      $('#btnXoa').removeClass('d-none');
    } 
  </script>

  <!-- Vendor js -->
  <script src="/assets/js/vendor.min.js"></script>
  <!-- Datatable plugin js -->
  <script src="/assets/libs/datatables/jquery.dataTables.min.js"></script>
  <script src="/assets/libs/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="/assets/libs/datatables/dataTables.responsive.min.js"></script>
  <script src="/assets/libs/datatables/responsive.bootstrap4.min.js"></script>
  <script src="/assets/libs/datatables/dataTables.buttons.min.js"></script>
  <script src="/assets/libs/datatables/buttons.bootstrap4.min.js"></script>
  <script src="/assets/libs/jszip/jszip.min.js"></script>
  <script src="/assets/libs/pdfmake/pdfmake.min.js"></script>
  <script src="/assets/libs/pdfmake/vfs_fonts.js"></script>
  <script src="/assets/libs/datatables/buttons.html5.min.js"></script>
  <script src="/assets/libs/datatables/buttons.print.min.js"></script>
  <script src="/assets/libs/datatables/dataTables.keyTable.min.js"></script>
  <script src="/assets/libs/datatables/dataTables.select.min.js"></script>
  <!-- Toastr js -->
  <script src="/assets/libs/toastr/toastr.min.js"></script>
  <script src="/assets/js/pages/toastr.init.js"></script>
  <!-- Datatables init -->
  <script src="/assets/js/pages/datatables.init.js"></script>
  <!-- Plugins js -->
  <script src="/assets/libs/dropify/dropify.min.js"></script>
  <!-- Init js-->
  <script src="/assets/js/pages/form-fileuploads.init.js"></script>
  <!-- select 2 -->
  <script src="/assets/libs/multiselect/jquery.multi-select.js"></script>
  <script src="/assets/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
  <script src="/assets/libs/select2/select2.min.js"></script>
  <script src="/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
  <!-- form advanced init -->
  <script src="/assets/js/pages/form-advanced.init.js"></script>
  <!-- App js -->
  <script src="/assets/js/app.min.js"></script>
  
  </body>
</html>   