import { observable, action, computed } from 'mobx'
import { gettext } from '~components/Language/src'

interface IMarks {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}

class ServerBenchmarkStore {
  public ID = 'serverBenchmark'

  @observable public isLoading: boolean = false
  @observable public linkText: string = gettext('Click to test')
  @observable public marks: IMarks = {
    floatLoop: 0,
    hash: 0,
    intLoop: 0,
    ioLoop: 0,
  }

  @action
  public setMarks = (marks: IMarks) => {
    this.marks = marks
  }

  @computed
  get linkTitle() {
    return Object.keys(this.marks)
      .map(key => {
        const marks = this.marks[key]

        return `${key}: ${marks}`
      })
      .join(' / ')
  }

  @computed
  get totalMarks(): number {
    return Object.values(this.marks).reduce((a: number, b: number) => {
      return a + b
    }, 0)
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
