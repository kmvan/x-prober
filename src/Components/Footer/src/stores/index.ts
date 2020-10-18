import conf from '@/Helper/src/components/conf'
import { configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

class FooterStore {
  public readonly ID = 'footer'
  public readonly conf = conf?.[this.ID]
}

export default new FooterStore()
