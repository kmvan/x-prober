export type PieChartStatusKey = 'Low' | 'Medium' | 'High';
export type PieChartStatusValue = 'low' | 'medium' | 'high';
export const PieChartStatus = {
  Low: 'low',
  Medium: 'medium',
  High: 'high',
} satisfies Record<PieChartStatusKey, PieChartStatusValue>;
