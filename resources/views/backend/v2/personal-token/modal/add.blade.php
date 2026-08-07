<div class="modal fade bs-modal-add" tabindex="-1" role="dialog" aria-labelledby="addTokenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTokenModalLabel">Tạo Token Cá Nhân</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm-add-token" class="form-horizontal needs-validation" novalidate method="POST" action="{{ route('backend.personal-token.ajax-add') }}">
                    <div class="mb-3">
                        <label class="form-label" for="token_name">Tên Token <span class="text-danger">(*)</span></label>
                        <input type="text" class="form-control" id="token_name" name="name" placeholder="Ví dụ: App Bán Hàng" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="token_permission">Quyền Hạn <span class="text-danger">(*)</span></label>
                        <select id="token_permission" name="permission" class="form-control" required>
                            <option value="read">Quyền đọc (Read-only)</option>
                            <option value="write">Quyền ghi (Read & Write)</option>
                        </select>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Tạo Token</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Đóng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal to display created token -->
<div class="modal fade bs-modal-show-token" tabindex="-1" role="dialog" aria-labelledby="showTokenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showTokenModalLabel">Token Đã Được Tạo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger font-size-13">Vui lòng sao chép token này ngay bây giờ. Bạn sẽ không thể nhìn lại nó sau khi đóng hộp thoại này.</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="created-token-val" readonly>
                    <button class="btn btn-primary" type="button" onclick="personalToken.copyCreatedToken()"><i class="fas fa-copy"></i> Sao chép</button>
                </div>
                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tôi đã sao chép</button>
                </div>
            </div>
        </div>
    </div>
</div>
