import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'

class FooterStore {
  public ID = 'footer'
  public conf = get(conf, this.ID)
}

export default new FooterStore()
