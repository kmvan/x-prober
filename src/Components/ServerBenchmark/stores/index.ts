import { configure, makeAutoObservable } from 'mobx'
import { ConfigStore } from '../../Config/stores'
import { AppConfigBenchmarkProps } from '../../Config/typings'
import { gettext } from '../../Language'
import type { ServerBenchmarkMarksProps } from '../typings'
configure({
  enforceActions: 'observed',
})
class Main {
  public isLoading = false
  public linkText: string = gettext('👆 Click to test')
  public marks: ServerBenchmarkMarksProps = {
    cpu: 0,
    read: 0,
    write: 0,
  }
  public constructor() {
    makeAutoObservable(this)
  }
  public get servers(): AppConfigBenchmarkProps[] | null {
    return ConfigStore?.appConfig?.BENCHMARKS || null
  }
  public setMarks = (marks: ServerBenchmarkMarksProps) => {
    this.marks = marks
  }
  public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }
  public setLinkText = (linkText: string) => {
    this.linkText = linkText
  }
}
export const ServerBenchmarkStore = new Main()
