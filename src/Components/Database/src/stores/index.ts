import { conf } from '@/Utils/src/components/conf'
import { configure } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'database'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
}
export const DatabaseStore = new Main()
