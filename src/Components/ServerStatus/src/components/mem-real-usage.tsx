import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '~components/Card/src/components/card-grid'
import { gettext } from '~components/Language/src'
import store from '../stores'
import ProgressBar from '~components/ProgressBar/src/components'

@observer
export default class MemRealUsage extends Component {
  public render() {
    const { max, value } = store.memRealUsage

    return (
      <CardGrid
        title={gettext(
          'Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.'
        )}
        name={gettext('Memory real usage')}
        tablet={[1, 1]}
      >
        <ProgressBar value={value} max={max} isCapacity={true} />
      </CardGrid>
    )
  }
}
