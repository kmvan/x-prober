export interface ServerToBrowserPingItemProps {
  id: string;
  time: number;
}
export interface ServerToBrowserPingProps {
  location: string;
  items: ServerToBrowserPingItemProps[];
}
