import { CardStore } from '../../Card/src/stores'
import { gettext } from '../../Language/src'
import { ServerStatus as component } from './components'
import { ServerStatusConstants } from './constants'
export const ServerStatusBoostrap = (): void => {
  const { id, isEnable } = ServerStatusConstants
  isEnable &&
    CardStore.addCard({
      id,
      title: gettext('Server Status'),
      tinyTitle: gettext('Status'),
      priority: 100,
      component,
    })
}
