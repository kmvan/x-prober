import { configure } from 'mobx'
import { conf } from '../../../Utils/src/components/conf'

configure({
  enforceActions: 'observed',
})
class Main {
  public readonly ID = 'footer'
  public readonly conf = conf?.[this.ID]
}
export const FooterStore = new Main()
