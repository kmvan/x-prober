import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'

class PhpExtensionsStore {
  public ID = 'phpExtensions'
  public conf = get(conf, this.ID)
}

export default new PhpExtensionsStore()
