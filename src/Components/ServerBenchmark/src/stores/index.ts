import { action, computed, configure, makeObservable, observable } from 'mobx'
import { ConfigStore } from '../../../Config/src/stores'
import { AppConfigBenchmarkProps } from '../../../Config/src/typings'
import { gettext } from '../../../Language/src'
import { conf } from '../../../Utils/src/components/conf'
import type { ServerBenchmarkMarksProps } from '../typings'

configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'serverBenchmark'
  public readonly conf = conf?.[this.ID]
  public readonly enabledMyServerBenchmark: boolean =
    !this.conf?.disabledMyServerBenchmark

  @observable public isLoading = false
  @observable public linkText: string = gettext('👆 Click to test')
  @observable public marks: ServerBenchmarkMarksProps = {
    cpu: 0,
    read: 0,
    write: 0,
  }

  public constructor() {
    makeObservable(this)
  }

  @computed public get servers(): AppConfigBenchmarkProps[] | null {
    return ConfigStore?.appConfig?.BENCHMARKS || null
  }

  @action public setMarks = (marks: ServerBenchmarkMarksProps) => {
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
