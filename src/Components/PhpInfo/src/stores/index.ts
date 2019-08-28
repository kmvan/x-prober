import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'

class PhpInfoStore {
  public ID = 'phpInfo'
  public conf = get(conf, this.ID)
}

export default new PhpInfoStore()
