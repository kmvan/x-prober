export interface TemperatureSensorItemProps {
  id: string;
  name: string;
  celsius: number;
}
export interface TemperatureSensorPollDataProps {
  items: TemperatureSensorItemProps[];
}
