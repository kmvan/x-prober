export interface AppConfigBenchmarkProps {
  name: string
  url: string
  date?: string
  proberUrl?: string
  binUrl?: string
  total?: number
  detail: {
    cpu: number
    read: number
    write: number
  }
}
export interface AppConfigProps {
  APP_VERSION: string
  BENCHMARKS: AppConfigBenchmarkProps[]
}
