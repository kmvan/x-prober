import { configure } from 'mobx'
import { conf } from '../../Utils/components/conf'
configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'myInfo'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = Boolean(this.conf)
}
export const MyInfoStore = new Main()
