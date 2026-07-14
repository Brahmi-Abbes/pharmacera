
namespace App\Support;

class SaleCart
{
    public static function addOrIncrement(array $items, int $medicineId, int $batchId, float $unitPrice, int $maxQuantity): array
    {
        foreach ($items as $key => $item) {
            if (($item['batch_id'] ?? null) === $batchId) {
                $items[$key]['quantity'] = min(($item['quantity'] ?? 0) + 1, $maxQuantity);
                $items[$key]['subtotal'] = round($items[$key]['quantity'] * $unitPrice, 2);
                return $items;
            }
        }

        $items[] = [
            'medicine_id' => $medicineId,
            'batch_id'    => $batchId,
            'quantity'    => 1,
            'unit_price'  => $unitPrice,
            'subtotal'    => $unitPrice,
        ];

        return $items;
    }

    public static function total(array $items): float
    {
        return round(collect($items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']), 2);
    }
}