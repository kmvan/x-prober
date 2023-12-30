import { configure, makeAutoObservable } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  public latestPhpVersion = ''
  public latestPhpDate = ''
  public constructor() {
    makeAutoObservable(this)
  }
  public setLatestPhpVersion = (latestPhpVersion: string) => {
    this.latestPhpVersion = latestPhpVersion
  }
  public setLatestPhpDate = (latestPhpDate: string) => {
    this.latestPhpDate = latestPhpDate
  }
}
export const PhpInfoStore = new Main()
