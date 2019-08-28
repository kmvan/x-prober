import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'

class DatabaseStore {
  public ID = 'database'
  public conf = get(conf, this.ID)
}

export default new DatabaseStore()
