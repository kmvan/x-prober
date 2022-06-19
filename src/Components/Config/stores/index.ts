import { configure, makeAutoObservable } from 'mobx'
import { BootstrapConstants } from '../../Bootstrap/constants'
import { gettext } from '../../Language'
import { ToastStore } from '../../Toast/stores'
import { AppConfigProps } from '../typings'
configure({
  enforceActions: 'observed',
})
class Main {
  public appConfig: AppConfigProps | null = null
  constructor() {
    makeAutoObservable(this)
    this.fetch()
  }
  private fetch = async () => {
    const { isDev, appConfigUrls, appConfigUrlDev } = BootstrapConstants
    let configStatus = false
    // dev version
    if (isDev) {
      await fetch(appConfigUrlDev)
        .then((res) => res.json())
        .then((res) => {
          this.setAppConfig(res)
        })
        .catch((e) => {
          console.warn(e)
        })
      return
    }
    // online version
    for (let i = 0; i < appConfigUrls.length; i += 1) {
      await fetch(appConfigUrls[i])
        .then((res) => res.json())
        // eslint-disable-next-line @typescript-eslint/no-loop-func
        .then((res) => {
          this.setAppConfig(res)
          configStatus = true
        })
        .catch((e) => {
          console.warn(e)
        })
      if (configStatus) {
        break
      }
    }
    if (!configStatus) {
      ToastStore.open(
        gettext(
          'Error: can not fetch remote config data, update checker is disabled.'
        )
      )
    }
  }
  public setAppConfig = (appConfig: AppConfigProps) => {
    this.appConfig = appConfig
  }
}
export const ConfigStore = new Main()
