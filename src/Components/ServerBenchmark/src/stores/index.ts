import { observable, action, computed } from 'mobx'
import conf from '~components/Helper/src/components/conf'

interface Imarks {
  floatLoop: number
  hash: number
  intLoop: number
  ioLoop: number
}

class ServerBenchmarkStore {
  public ID = 'serverBenchmark'
  public conf = conf[this.ID]

  @observable public isLoading: boolean = false
  @observable public linkText: string = this.conf.lang.goTest
  @observable public marks: Imarks = {
    floatLoop: 0,
    hash: 0,
    intLoop: 0,
    ioLoop: 0,
  }

  @action
  public setMarks = (marks: Imarks) => {
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
