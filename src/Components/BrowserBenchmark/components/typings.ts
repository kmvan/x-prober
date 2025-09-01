export interface BrowserBenchmarkMarksProps {
  sunSpider: number;
  hash: number;
  object: number;
  cssAnimation: number;
  gc: number;
  canvas: number;
  webgl: number;
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
