import conf from '@/Utils/src/components/conf'
import { configure } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Store {
  public readonly ID = 'footer'
  public readonly conf = conf?.[this.ID]
}
const FooterStore = new Store()
export default FooterStore
