import { observable, action, configure } from 'mobx'
import ToastStore from '~components/Toast/src/stores'
import { gettext } from '~components/Language/src'
import BootstrapStore from '~components/Bootstrap/src/stores'

configure({
  enforceActions: 'observed',
})

export interface IAppConfigBenchmark {
  name: string
  url: string
  date?: string
  proberUrl?: string
  binUrl?: string
  total?: number
  detail: {
    hash: number
    intLoop: number
    floatLoop: number
    ioLoop: number
  }
}

interface IAppConfig {
  APP_VERSION: string
  BENCHMARKS: IAppConfigBenchmark[]
}

class ConfigStore {
  @observable public appConfig: IAppConfig | null = null

  constructor() {
    this.fetch()
  }

  private fetch = async () => {
    const { isDev, appConfigUrl, appConfigUrlDev } = BootstrapStore

    await fetch(isDev ? appConfigUrlDev : appConfigUrl)
      .then(res => res.json())
      .then(res => {
        this.setAppConfig(res)
      })
      .catch(e => {
        ToastStore.open(
          gettext(
            'Error: can not fetch remote config data, update checker is disabled.'
          )
        )
      })
  }

  @action
  public setAppConfig = (appConfig: IAppConfig) => {
    this.appConfig = appConfig
  }
}

export default new ConfigStore()
