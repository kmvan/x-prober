export interface BrowserBenchmarkMarksProps {
  js: number;
  dom: number;
  canvas: number;
}
export interface BrowserBenchmarkProps {
  id: string;
  name: string;
  version: string;
  ua: string;
  date: string;
  total: number;
  detail: BrowserBenchmarkMarksProps;
}
