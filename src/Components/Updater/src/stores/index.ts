import { action, computed, configure, makeObservable, observable } from 'mobx'
import { BootstrapStore } from '../../../Bootstrap/src/stores'
import { ConfigStore } from '../../../Config/src/stores'
import { gettext } from '../../../Language/src'
import { template } from '../../../Utils/src/components/template'
import { versionCompare } from '../../../Utils/src/components/version-compare'

configure({
  enforceActions: 'observed',
})
class Main {
  @observable public isUpdating = false

  @observable public isUpdateError = false

  public constructor() {
    makeObservable(this)
  }

  @computed public get newVersion(): string {
    const { appConfig } = ConfigStore
    if (!appConfig || !appConfig.APP_VERSION) {
      return ''
    }
    return versionCompare(BootstrapStore.version, appConfig.APP_VERSION) === -1
      ? appConfig.APP_VERSION
      : ''
  }

  @action public setIsUpdating = (isUpdating: boolean) => {
    this.isUpdating = isUpdating
  }

  @action public setIsUpdateError = (isUpdateError: boolean) => {
    this.isUpdateError = isUpdateError
  }

  @computed public get notiText(): string {
    if (this.isUpdating) {
      return gettext('⏳ Updating, please wait a second...')
    }
    if (this.isUpdateError) {
      return gettext('❌ Update error, click here to try again?')
    }
    if (this.newVersion) {
      return template(
        gettext('✨ Found update! Version {{oldVersion}} → {{newVersion}}'),
        {
          oldVersion: BootstrapStore.version,
          newVersion: this.newVersion,
        }
      )
    }
    return ''
  }
}
export const UpdaterStore = new Main()
