import conf from '@/Helper/src/components/conf'
import { configure } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Store {
  public readonly ID = 'database'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
}
const DatabaseStore = new Store()
export default DatabaseStore
