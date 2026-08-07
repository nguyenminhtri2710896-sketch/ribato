<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserDebitService;
use App\Services\UserService;
use App\Utilities\General;

class UserDebitController extends BaseController
{

    private $userDebitService = null;
    private $userService = null;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserDebitService $userDebitService, UserService $userService)
    {
        $this->userDebitService = $userDebitService;
        $this->userService = $userService;
    }

    public function getList()
    {
        $arrParams = request(['page', 'limit', 'query', 'sort']);

        if (!auth()->user()->full_access && !auth()->user()->is_accountant) {
            $arrParams["query"]["user_debits.user_id"] = auth()->user()->id;
        }
        return response()->json($this->userDebitService->getList($arrParams));
    }

    public function add()
    {
        $arrParams = request([
            'user_id',
            'type_id',
            'amount',
            'note',
            'debit_at',
        ]);
        $arrParams["user_create_id"] = auth()->user()->id;
        return response()->json($this->userDebitService->add($arrParams));

    }

    public function return()
    {
        $arrParams = request([
            'user_id',
            'type_id',
            'amount',
            'note',
            'debit_at',
        ]);
        $arrParams["user_create_id"] = auth()->user()->id;
        $arrParams["amount"] = abs($arrParams["amount"] ?? "") * -1;
        return response()->json($this->userDebitService->add($arrParams));

    }

    public function delete()
    {
        $arrParams = request(['id']);
        $arrParams["user_delete_id"] = auth()->user()->id;
        return response()->json($this->userDebitService->delete($arrParams));
    }
}