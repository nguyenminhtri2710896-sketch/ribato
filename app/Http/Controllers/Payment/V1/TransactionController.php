<?php

namespace App\Http\Controllers\Payment\V1;

use App\Services\BankService;
use App\Services\TransactionService;
use App\Services\UserBankAccountService;

class TransactionController extends BaseController
{


        /**
         * Create a new controller instance.
         *
         * @return void
         */
        protected $transactionService;
        protected $bankService;
        protected $userBankAccountService;
        public function __construct(TransactionService $transactionService, BankService $bankService, UserBankAccountService $userBankAccountService)
        {
                $this->transactionService     = $transactionService;
                $this->bankService            = $bankService;
                $this->userBankAccountService = $userBankAccountService;
        }

        public function index()
        {
                return "";
        }

        public function paymentMethod($strHash = "")
        {
                $objTransaction          = null;
                $objUserBankAccounts     = null;
                $resultTransactionDetail = $this->transactionService->getDetail(["query" => ["code_hashed" => $strHash]]);
                if ($resultTransactionDetail["error_code"] != 0) {
                        return redirect()->route('payment.transaction.error')->with(["message" => $resultTransactionDetail["message"]]);
                }

                $objTransaction = $resultTransactionDetail["data"]["transaction"];
                if (strtotime($objTransaction->expired_at) < time()) {
                        return redirect()->route('payment.transaction.error')->with(["message" => "Giao dịch đã hết hạn"]);
                }

                if ($objTransaction->status_id == 2) {
                        return redirect()->route('payment.transaction.error')->with(["message" => "Giao dịch đã thanh toán thành công trước đó."]);
                }


                if (!empty($objTransaction->bank_account_id)) {
                        return redirect()->route('payment.transaction.payment-bank', ['hash' => $strHash, "bank" => $objTransaction->bank_short_code]);
                }

                if (request()->isMethod("post")) {
                        /**
                         * Xử lý nhận data post bank lên để cập nhật lại
                         */
                        $arrParam                = request()->all();
                        $resultChonsePaymentBank = $this->transactionService->chonsePaymentBank(["code_hashed" => $strHash, "bank_account_id" => $arrParam["bank_account_id"] ?? 0]);
                        if ($resultChonsePaymentBank["error_code"] != 0) {
                                if ($resultChonsePaymentBank["error_code"] == 800) {
                                        return redirect()->route('payment.transaction.payment-bank', ['hash' => $strHash, "bank" => $objTransaction->bank_short_code]);
                                }
                                return redirect()->route('payment.transaction.error')->with(["message" => $resultChonsePaymentBank["message"]]);
                        }

                        $objTransaction = $resultChonsePaymentBank["data"]["transaction"];
                        return redirect()->route('payment.transaction.payment-bank', ['hash' => $strHash, "bank" => $objTransaction->bank_short_code]);

                }


                $resultUserBankAccount = $this->userBankAccountService->getList(["query" => ["user_id" => $objTransaction->user_id, "user_bank_accounts.status_id" => 2, "bank_accounts.status_id" => 2]]);
                if ($resultUserBankAccount["error_code"] == 0) {
                        $objUserBankAccounts = $resultUserBankAccount["data"]["user_bank_accounts"];
                }

                return view("payment.v1.transaction.payment-method")->with(["strHash" => $strHash, "objTransaction" => $objTransaction, "objUserBankAccounts" => $objUserBankAccounts]);
        }

        public function paymentBank($strBankCode, $strHash = "")
        {
                $objTransaction          = null;
                $objUserBankAccounts     = null;
                $resultTransactionDetail = $this->transactionService->getDetail(["query" => ["code_hashed" => $strHash]]);
                if ($resultTransactionDetail["error_code"] == 0) {
                        $objTransaction = $resultTransactionDetail["data"]["transaction"];
                        if (strtotime($objTransaction->expired_at) < time()) {
                                return redirect()->route('payment.transaction.error')->with(["message" => "Giao dịch đã hết hạn"]);
                        }

                        if ($objTransaction->status_id == 2) {
                                return redirect()->route('payment.transaction.error')->with(["message" => "Giao dịch đã thanh toán thành công trước đó."]);
                        }

                        $resultUserBankAccount = $this->userBankAccountService->getList(["query" => ["user_id" => $objTransaction->user_id, "user_bank_accounts.status_id" => 2, "bank_accounts.status_id" => 2]]);
                        if ($resultUserBankAccount["error_code"] == 0) {
                                $objUserBankAccounts = $resultUserBankAccount["data"]["user_bank_accounts"];
                        }
                }

                return view("payment.v1.transaction.payment-bank")->with(["objTransaction" => $objTransaction, "objUserBankAccounts" => $objUserBankAccounts]);
        }

        public function error()
        {
                return view("payment.v1.transaction.error")->with([]);
        }


        public function checkComplete()
        {
                $arrParram               = request()->all();
                $strHash                 = $arrParram["hash"] ?? "";
                $resultTransactionDetail = $this->transactionService->getDetail(["query" => ["code_hashed" => $strHash]]);
                if ($resultTransactionDetail["error_code"] != 0) {
                        return response()->json($resultTransactionDetail);
                }
                $objTransaction = $resultTransactionDetail["data"]["transaction"];
                unset($resultTransactionDetail["data"]["transaction"]);
                $resultTransactionDetail["data"]["status_id"] = $objTransaction->status_id;
                return response()->json($resultTransactionDetail);
        }


}