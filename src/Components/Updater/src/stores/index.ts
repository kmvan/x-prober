import { configure, makeAutoObservable } from 'mobx'
import { BootstrapConstants } from '../../../Bootstrap/constants'
import { ConfigStore } from '../../../Config/src/stores'
import { gettext } from '../../../Language/src'
import { template } from '../../../Utils/src/components/template'
import { versionCompare } from '../../../Utils/src/components/version-compare'

configure({
  enforceActions: 'observed',
})
class Main {
  public isUpdating = false

  public isUpdateError = false

  public constructor() {
    makeAutoObservable(this)
  }

  public get newVersion(): string {
    const { appConfig } = ConfigStore
    if (!appConfig || !appConfig.APP_VERSION) {
      return ''
    }
    return versionCompare(BootstrapConstants.version, appConfig.APP_VERSION) ===
      -1
      ? appConfig.APP_VERSION
      : ''
  }

  public setIsUpdating = (isUpdating: boolean) => {
    this.isUpdating = isUpdating
  }

  public setIsUpdateError = (isUpdateError: boolean) => {
    this.isUpdateError = isUpdateError
  }

  public get notiText(): string {
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
          oldVersion: BootstrapConstants.version,
          newVersion: this.newVersion,
        }
      )
    }
    return ''
  }
}
export const UpdaterStore = new Main()
