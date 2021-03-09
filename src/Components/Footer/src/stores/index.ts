import { conf } from '@/Utils/src/components/conf'
import { configure } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'footer'
  public readonly conf = conf?.[this.ID]
}
export const FooterStore = new Main()
