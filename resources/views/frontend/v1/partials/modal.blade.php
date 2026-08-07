<div class="footer_mobile show-mobile">
    <div class="footer_mobile-inner">
        <div class="row row-32 align-items-center">
            <div class="col">
                <div class="inline-block">
                    <!-- button.button -->
                    <a class="ubg-ghost ubox-size-button-sm ubg-hover ubg-active ubtn"
                        href="/Transaction/PaymentMethod.html?token=45a14974d57c4ea4b38ce76efb205ca9&amp;vnp_Locale=en-US">
                        <div class="ubtn-inner">
                            <span class="ubtn-ic ubtn-ic-left">
                                <img src="{{ asset('payment/v1/image/icon/vi.svg') }}" alt="" class="ic-xl">
                            </span>
                            <span class="ubtn-text">Vi</span>
                        </div>
                    </a>
                    <!-- end button.button -->
                </div>
            </div>
           
        </div>
    </div>
</div>
<div class="modal fade text-center" id="modalCancelPayment" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-size-alert-default modal-dialog-scrollable modal-alert"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-wrap">
                    <div class="row row-16 modal-title-wrap">
                        <div class="col-12 text-center">
                            <h2 class="modal-title h2">
                                Hủy thanh to&#225;n
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body color-sub-default fz-h3">
                Qu&#253; kh&#225;ch c&#243; chắc chắn muốn hủy thanh to&#225;n giao dịch n&#224;y?
            </div>
            <div class="modal-footer justify-content-center">
                <!-- button.btnGroup -->
                <div
                    class="ubtn-group list-mb16 list-crop row row-16 justify-content-center group-col-md-3 group-col-fill">
                    <div class="group-col-item">
                        <!-- button.button -->
                        <a data-bs-dismiss="modal"
                            class="ubg-secondary ubox-size-button-default ubg-hover ubg-active ubtn">
                            <div class="ubtn-inner">
                                <span class="ubtn-text">Đ&#243;ng</span>
                            </div>
                        </a>
                        <!-- end button.button -->
                    </div>
                    <div class="group-col-item">
                        <!-- button.button -->
                        <a data-bs-dismiss="modal" href="#"
                            class="ubg-danger ubox-size-button-default ubg-hover ubg-active ubtn" id="btnCancelModal">
                            <div class="ubtn-inner">
                                <span class="ubtn-text">X&#225;c nhận hủy</span>
                            </div>
                        </a>
                        <!-- end button.button -->
                    </div>
                </div>
                <!-- end button.btnGroup -->
            </div>
        </div>
    </div>
</div>
