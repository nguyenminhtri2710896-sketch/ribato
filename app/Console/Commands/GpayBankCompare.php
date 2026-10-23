<?php

namespace App\Console\Commands;

use App\Models\Bank;
use App\Models\GatewayAccount;
use App\Utilities\GpayV2;
use Illuminate\Console\Command;

class GpayBankCompare extends Command
{
    /**
     * @var string
     */
    protected $signature = 'gpay:bank-compare {--gateway_account_id= : ID gateway_account (mặc định lấy gateway_id=8 đầu tiên)} {--env= : sandbox|production}';

    /**
     * @var string
     */
    protected $description = 'Lấy danh sách ngân hàng từ Gpay (/reference/banks) và so sánh với bảng banks để tìm bank còn thiếu';

    public function handle()
    {
        $id = $this->option('gateway_account_id');
        $account = $id
            ? GatewayAccount::find($id)
            : GatewayAccount::where('gateway_id', 8)->first();

        if (!$account) {
            $this->error('Không tìm thấy gateway account của Gpay (gateway_id = 8)');
            return 1;
        }

        $res = GpayV2::fromGatewayAccount($account, $this->option('env') ?: null)->getBankList();
        if (empty($res['success'])) {
            $this->error('Gọi Gpay thất bại: ' . json_encode($res, JSON_UNESCAPED_UNICODE));
            return 1;
        }

        $gpayBanks = $res['banks'];
        // Có thể bị bọc thêm 1 lớp (vd: data.banks / data.items)
        foreach (['banks', 'items', 'list', 'data'] as $key) {
            if (is_array($gpayBanks) && isset($gpayBanks[$key]) && is_array($gpayBanks[$key])) {
                $gpayBanks = $gpayBanks[$key];
                break;
            }
        }
        $gpayBanks = array_values((array) $gpayBanks);
        $this->info('Gpay trả về ' . count($gpayBanks) . ' ngân hàng');

        $dbBanks = Bank::all();
        $byBin = $dbBanks->filter(fn($b) => $b->napas_code)->keyBy(fn($b) => strtoupper(trim($b->napas_code)));
        $byCode = $dbBanks->filter(fn($b) => $b->short_code)->keyBy(fn($b) => strtoupper(trim($b->short_code)));
        $this->info('Bảng banks có ' . $dbBanks->count() . ' ngân hàng');

        $missing = [];
        $matchedIds = [];
        foreach ($gpayBanks as $g) {
            $code = strtoupper(trim((string) ($g['bank_code'] ?? '')));
            $bin = strtoupper(trim((string) ($g['bank_bin'] ?? '')));

            // Ưu tiên khớp theo BIN (napas_code), sau đó theo bank_code (short_code)
            $found = ($bin !== '' ? $byBin->get($bin) : null) ?: ($code !== '' ? $byCode->get($code) : null);
            if ($found) {
                $matchedIds[$found->id] = true;
                continue;
            }
            $missing[] = [$code, $bin, $g['bank_name'] ?? ''];
        }

        if ($missing) {
            $this->warn('Thiếu trong bảng banks: ' . count($missing));
            $this->table(['bank_code', 'bank_bin', 'bank_name'], $missing);
        } else {
            $this->info('Bảng banks đã có đủ tất cả ngân hàng của Gpay');
        }

        $extra = $dbBanks->filter(fn($b) => !isset($matchedIds[$b->id]))
            ->map(fn($b) => [$b->id, $b->short_code, $b->napas_code, $b->name])->values()->all();
        $this->line('Có trong banks nhưng Gpay không có: ' . count($extra));
        if ($extra && $this->output->isVerbose()) {
            $this->table(['id', 'short_code', 'napas_code', 'name'], $extra);
        }

        return 0;
    }
}
