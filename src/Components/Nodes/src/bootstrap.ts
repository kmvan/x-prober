import { CardStore } from '../../Card/src/stores'
import { gettext } from '../../Language/src'
import { Nodes as component } from './components'
import { NodesConstants } from './constants'
export const NodesBoostrap = (): void => {
  const { id, isEnable, conf } = NodesConstants
  isEnable &&
    conf?.items?.length &&
    CardStore.addCard({
      id,
      title: gettext('Nodes'),
      tinyTitle: gettext('Nodes'),
      priority: 50,
      component,
    })
}
