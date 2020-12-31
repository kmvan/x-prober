import React, { MouseEvent, useCallback } from 'react'
import store from '../stores'
import { gettext } from '@/Language/src'
import Row from '@/Grid/src/components/row'
import CardGrid from '@/Card/src/components/card-grid'
import serverFetch from '@/Fetch/src/server-fetch'
import { OK, TOO_MANY_REQUESTS } from '@/Restful/src/http-status'
import template from '@/Helper/src/components/template'
import CardError from '@/Card/src/components/error'
import CardRuby from '@/Card/src/components/card-ruby'
import { toJS } from 'mobx'
import { observer } from 'mobx-react-lite'

const Result = ({
  hash,
  intLoop,
  floatLoop,
  ioLoop,
  date,
}: {
  hash: number
  intLoop: number
  floatLoop: number
  ioLoop: number
  date?: string
}) => {
  return (
    <>
      <CardRuby ruby={hash.toLocaleString()} rt='HASH' />
      {' + '}
      <CardRuby ruby={intLoop.toLocaleString()} rt='INT' />
      {' + '}
      <CardRuby ruby={floatLoop.toLocaleString()} rt='FLOAT' />
      {' + '}
      <CardRuby ruby={ioLoop.toLocaleString()} rt='IO' />
      {' = '}
      <CardRuby
        isResult={true}
        ruby={(hash + intLoop + floatLoop + ioLoop).toLocaleString()}
        rt={date || ''}
      />
    </>
  )
}

const Items = observer(() => {
  const { servers } = store

  if (!servers) {
    return (
      <CardError>{gettext('Can not fetch marks data from GitHub.')}</CardError>
    )
  }

  const items = toJS(servers).map((item) => {
    item.total = item.detail
      ? Object.values(item.detail).reduce((a, b) => a + b, 0)
      : 0

    return item
  })

  items.sort((a, b) => Number(b.total) - Number(a.total))

  const results = items.map(
    ({ name, url, date, proberUrl, binUrl, detail }) => {
      if (!detail) {
        return
      }

      const { hash, intLoop, floatLoop, ioLoop } = detail || {
        hash: 0,
        intLoop: 0,
        floatLoop: 0,
        ioLoop: 0,
      }

      const proberLink = proberUrl ? (
        <a
          href={proberUrl}
          target='_blank'
          title={gettext('Visit prober page')}>
          {' 🔗 '}
        </a>
      ) : (
        ''
      )

      const binLink = binUrl ? (
        <a href={binUrl} target='_blank' title={gettext('Download speed test')}>
          {' ⬇️ '}
        </a>
      ) : (
        ''
      )

      const title = (
        <a
          href={url}
          target='_blank'
          title={gettext('Visit the official website')}>
          {name}
        </a>
      )

      return (
        <CardGrid
          key={name}
          name={title}
          tablet={[1, 2]}
          desktopMd={[1, 3]}
          desktopLg={[1, 4]}>
          <Result
            {...{
              hash,
              intLoop,
              floatLoop,
              ioLoop,
              date,
            }}
          />
          {proberLink}
          {binLink}
        </CardGrid>
      )
    }
  )

  return <>{results}</>
})

const TestBtn = observer(
  ({ onClick }: { onClick: (e: MouseEvent<HTMLAnchorElement>) => void }) => {
    const { marks, linkText } = store
    const marksText = marks ? <Result {...marks} /> : ''

    return (
      <CardGrid
        name={gettext('My server')}
        tablet={[1, 2]}
        desktopMd={[1, 3]}
        desktopLg={[1, 4]}>
        <a href='#' onClick={onClick}>
          <div>{linkText}</div>
          <div>{marksText}</div>
        </a>
      </CardGrid>
    )
  }
)

const ServerBenchmark = observer(() => {
  const onClick = useCallback(async (e: MouseEvent<HTMLAnchorElement>) => {
    e.preventDefault()

    const { isLoading, setIsLoading, setMarks, setLinkText } = store

    if (isLoading) {
      return false
    }

    setLinkText(gettext('⏳ Testing, please wait...'))
    setIsLoading(true)

    const { data = {}, status } = await serverFetch('benchmark')
    const { marks, seconds } = data

    if (status === OK) {
      if (marks) {
        setMarks(marks)
        setLinkText('')
      } else {
        setLinkText(gettext('Network error, please try again later.'))
      }
    } else if (status === TOO_MANY_REQUESTS) {
      const secondsMsg = template(gettext('⏳ Please wait ${seconds}s'), {
        seconds,
      })
      setLinkText(secondsMsg)
    } else {
      setLinkText(gettext('Network error, please try again later.'))
    }

    setIsLoading(false)
  }, [])

  return (
    <Row>
      {store.enabledMyServerBenchmark && <TestBtn onClick={onClick} />}
      <Items />
    </Row>
  )
})

export default ServerBenchmark
