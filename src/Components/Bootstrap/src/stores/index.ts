import { conf } from '@/Utils/src/components/conf'
import { action, configure, makeObservable, observable } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'bootstrap'
  public readonly conf = conf?.[this.ID]
  public readonly version: string = this.conf?.version
  public readonly appConfigUrls: string[] = this.conf?.appConfigUrls
  public readonly appConfigUrlDev: string = this.conf?.appConfigUrlDev
  public readonly appName: string = this.conf?.appName
  public readonly appUrl: string = this.conf?.appUrl
  public readonly authorUrl: string = this.conf?.authorUrl
  public readonly authorName: string = this.conf?.authorName
  public readonly isDev: boolean = this.conf?.isDev
  @observable public appContainer: HTMLElement | null = null
  public constructor() {
    makeObservable(this)
  }
  @action public setAppContainer = (appContainer: HTMLElement | null) => {
    this.appContainer = appContainer
  }
}
export const BootstrapStore = new Main()
