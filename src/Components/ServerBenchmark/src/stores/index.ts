import conf from '@/Helper/src/components/conf'
import ConfigStore, { AppConfigBenchmarkProps } from '@/Config/src/stores'
import {
  action,
  computed,
  configure,
  makeObservable,
  observable
  } from 'mobx'
import { gettext } from '@/Language/src'
configure({
  enforceActions: 'observed',
})
export interface MarksProps {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}
class Store {
  public readonly ID = 'serverBenchmark'
  public readonly conf = conf?.[this.ID]
  public readonly enabledMyServerBenchmark: boolean = !this.conf
    ?.disabledMyServerBenchmark
  @observable public isLoading: boolean = false
  @observable public linkText: string = gettext('Click to test')
  @observable public marks: MarksProps | null = null
  public constructor() {
    makeObservable(this)
  }
  @computed
  public get servers(): AppConfigBenchmarkProps[] | null {
    return ConfigStore?.appConfig?.BENCHMARKS || null
  }
  @action
  public setMarks = (marks: MarksProps) => {
    this.marks = marks
  }
  @action
  public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }
  @action
  public setLinkText = (linkText: string) => {
    this.linkText = linkText
  }
}
const ServerBenchmarkStore = new Store()
export default ServerBenchmarkStore
