import { action, configure, makeObservable, observable } from 'mobx'
import { conf } from '../../../Utils/src/components/conf'

configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'phpInfo'

  public readonly conf = conf?.[this.ID]

  public readonly enabled: boolean = Boolean(this.conf)

  @observable public latestPhpVersion = ''

  @observable public latestPhpDate = ''

  public constructor() {
    makeObservable(this)
  }

  @action public setLatestPhpVersion = (latestPhpVersion: string) => {
    this.latestPhpVersion = latestPhpVersion
  }

  @action public setLatestPhpDate = (latestPhpDate: string) => {
    this.latestPhpDate = latestPhpDate
  }
}
export const PhpInfoStore = new Main()
