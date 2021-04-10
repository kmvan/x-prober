import { AppConfigBenchmarkProps, ConfigStore } from '@/Config/src/stores'
import { gettext } from '@/Language/src'
import { conf } from '@/Utils/src/components/conf'
import { action, computed, configure, makeObservable, observable } from 'mobx'
configure({
  enforceActions: 'observed',
})
export interface MarksProps {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}
class Main {
  public readonly ID = 'serverBenchmark'
  public readonly conf = conf?.[this.ID]
  public readonly enabledMyServerBenchmark: boolean = !this.conf
    ?.disabledMyServerBenchmark
  @observable public isLoading: boolean = false
  @observable public linkText: string = gettext('👆 Click to test')
  @observable public marks: MarksProps = {
    hash: 0,
    intLoop: 0,
    floatLoop: 0,
    ioLoop: 0,
  }
  public constructor() {
    makeObservable(this)
  }
  @computed public get servers(): AppConfigBenchmarkProps[] | null {
    return ConfigStore?.appConfig?.BENCHMARKS || null
  }
  @action public setMarks = (marks: MarksProps) => {
    this.marks = marks
  }
  @action public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }
  @action public setLinkText = (linkText: string) => {
    this.linkText = linkText
  }
}
export const ServerBenchmarkStore = new Main()
