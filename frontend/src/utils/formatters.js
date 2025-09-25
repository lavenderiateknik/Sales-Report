export function formatCurrency(value) {
  const num = Number(value);
  if (!Number.isFinite(num)) return '-';

  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(num);
}
