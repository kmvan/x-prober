import { observable, action, configure, computed } from 'mobx'
import { gettext } from '~components/Language/src'
import ConfigStore, {
  AppConfigBenchmarkProps,
} from '~components/Config/src/stores'
import conf from '~components/Helper/src/components/conf'

configure({
  enforceActions: 'observed',
})

export interface MarksProps {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}

class ServerBenchmarkStore {
  public readonly ID = 'serverBenchmark'
  public readonly conf = conf?.[this.ID]
  public readonly enabledMyServerBenchmark: boolean = !this.conf
    ?.disabledMyServerBenchmark

  @observable public isLoading: boolean = false
  @observable public linkText: string = gettext('Click to test')
  @observable public marks: MarksProps | null = null

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

export default new ServerBenchmarkStore()
