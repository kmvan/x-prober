export interface DiskUsageItemProps {
  id: string;
  total: number;
  free: number;
}
export interface DiskUsagePollDataProps {
  items: DiskUsageItemProps[];
}
