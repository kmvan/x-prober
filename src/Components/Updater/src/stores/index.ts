import { observable, action, computed, configure, makeObservable } from 'mobx'
import { gettext } from '@/Language/src'
import BootstrapStore from '@/Bootstrap/src/stores'
import ConfigStore from '@/Config/src/stores'
import versionCompare from '@/Helper/src/components/version-compare'
import template from '@/Helper/src/components/template'

configure({
  enforceActions: 'observed',
})

class Store {
  @observable public isUpdating: boolean = false
  @observable public isUpdateError: boolean = false

  public constructor() {
    makeObservable(this)
  }

  @computed
  public get newVersion(): string {
    const { appConfig } = ConfigStore

    if (!appConfig || !appConfig.APP_VERSION) {
      return ''
    }

    return versionCompare(BootstrapStore.version, appConfig.APP_VERSION) === -1
      ? appConfig.APP_VERSION
      : ''
  }

  @action
  public setIsUpdating = (isUpdating: boolean) => {
    this.isUpdating = isUpdating
  }

  @action
  public setIsUpdateError = (isUpdateError: boolean) => {
    this.isUpdateError = isUpdateError
  }

  @computed
  public get notiText(): string {
    if (this.isUpdating) {
      return gettext('⏳ Updating, please wait a second...')
    }

    if (this.isUpdateError) {
      return gettext('❌ Update error, click here to try again?')
    }

    if (this.newVersion) {
      return template(
        gettext('✨ Found update! Version ${oldVersion} → ${newVersion}'),
        {
          oldVersion: BootstrapStore.version,
          newVersion: this.newVersion,
        }
      )
    }

    return ''
  }
}

const UpdaterStore = new Store()

export default UpdaterStore
