import { observable, action, configure, computed } from 'mobx'
import { gettext } from '~components/Language/src'
import ConfigStore, { IAppConfigBenchmark } from '~components/Config/src/stores'
import { get } from 'lodash-es'

configure({
  enforceActions: 'observed',
})

export interface IMarks {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}

class ServerBenchmarkStore {
  public readonly ID = 'serverBenchmark'

  @observable public isLoading: boolean = false
  @observable public linkText: string = gettext('Click to test')
  @observable public marks: IMarks | null = null

  @computed
  get servers(): IAppConfigBenchmark[] | null {
    return get(ConfigStore, 'appConfig.BENCHMARKS') || null
  }

  @action
  public setMarks = (marks: IMarks) => {
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
