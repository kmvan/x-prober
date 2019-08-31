import { observable, action } from 'mobx'
import { gettext } from '~components/Language/src'

export interface IMarks {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}

class ServerBenchmarkStore {
  public ID = 'serverBenchmark'

  @observable public isLoading: boolean = false
  @observable public linkText: string = gettext('Click to test')
  @observable public marks: IMarks | null = null

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
