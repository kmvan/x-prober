import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'

class BootstrapStore {
  public ID = 'bootstrap'
  public conf = get(conf, this.ID)
  public version = get(this.conf, 'version')
}

export default new BootstrapStore()
