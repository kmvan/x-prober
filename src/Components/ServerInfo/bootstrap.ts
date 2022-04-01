import { CardStore } from '../Card/stores'
import { gettext } from '../Language'
import { ServerInfo as component } from './components'
import { ServerInfoStore } from './stores'
export const ServerInfoBoostrap = (): void => {
  ServerInfoStore.enabled &&
    CardStore.addCard({
      id: ServerInfoStore.ID,
      title: gettext('Server Information'),
      tinyTitle: gettext('Info'),
      priority: 300,
      component,
    })
}
