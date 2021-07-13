import fetch from 'isomorphic-unfetch'
import { action, configure, makeObservable, observable } from 'mobx'
import { BootstrapStore } from '../../../Bootstrap/src/stores'
import { gettext } from '../../../Language/src'
import { ToastStore } from '../../../Toast/src/stores'
import { AppConfigProps } from '../typings'
configure({
  enforceActions: 'observed',
})
class Main {
  @observable public appConfig: AppConfigProps | null = null
  constructor() {
    makeObservable(this)
    this.fetch()
  }

  private fetch = async () => {
    const { isDev, appConfigUrls, appConfigUrlDev } = BootstrapStore
    let configStatus = false
    // dev version
    if (isDev) {
      await fetch(appConfigUrlDev)
        .then((res) => res.json())
        .then((res) => {
          this.setAppConfig(res)
        })
        .catch((e) => {
          console.error(e)
        })
      return
    }
    // online version
    for (let i = 0; i < appConfigUrls.length; i++) {
      await fetch(appConfigUrls[i])
        .then((res) => res.json())
        .then((res) => {
          this.setAppConfig(res)
          configStatus = true
        })
        .catch((e) => {
          console.error(e)
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

  @action public setAppConfig = (appConfig: AppConfigProps) => {
    this.appConfig = appConfig
  }
}
export const ConfigStore = new Main()
