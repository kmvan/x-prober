import React, { Component } from 'react'
import { observer } from 'mobx-react'
import CardGrid from '@/Card/src/components/card-grid'
import { gettext } from '@/Language/src'
import store from '../stores'
import ProgressBar from '@/ProgressBar/src/components'

@observer
export default class MemBuffers extends Component {
  public render() {
    const { max, value } = store.memBuffers

    return (
      <CardGrid
        title={gettext(
          'Buffers are in-memory block I/O buffers. They are relatively short-lived. Prior to Linux kernel version 2.4, Linux had separate page and buffer caches. Since 2.4, the page and buffer cache are unified and Buffers is raw disk blocks not represented in the page cache—i.e., not file data.'
        )}
        name={gettext('Memory buffers')}
        tablet={[1, 2]}
      >
        <ProgressBar value={value} max={max} isCapacity />
      </CardGrid>
    )
  }
}
