import React from 'react'
import {Text, View} from '@react-pdf/renderer'
import styles from './styles.js'
import colors from './colors.js'
import icons from './icons.js'

function addKey(child, idx) {
    return {...child, key: idx}
}

function ucfirst(text) {
    if (!text || typeof text !== 'string') return text
    return text.charAt(0).toUpperCase() + text.slice(1)
}

function FeedbackContents({feedbackContents, observations, requirements, statuses}) {
    if (!feedbackContents) return <View/>

    function transformToReactPdf(node) {
        if (node.type === 'doc') {
            return <View>{node.content.map(transformToReactPdf).map(addKey)}</View>
        }

        if (node.type === 'paragraph') {
            if (!node.content || node.content.length === 0) {
                return <Text style={styles.p}> </Text>
            }
            return <Text style={styles.p}>{node.content.map(transformToReactPdf).map(addKey)}</Text>
        }

        if (node.type === 'text') {
            return <Text>{node.text}</Text>
        }

        if (node.type === 'heading') {
            if (![3, 5, 6].includes(node.attrs.level)) {
                return <View />
            }
            const style = styles[`h${node.attrs.level}`]

            if (!node.content || node.content.length === 0) {
                return <Text style={style}> </Text>
            }
            return <Text minPresenceAhead={30} style={style}>{node.content.map(transformToReactPdf).map(addKey)}</Text>
        }

        if (node.type === 'observation') {
            const observation = observations.find(o => o.pivot.id === node.attrs.id)

            if (!observation) {
                return <View />
            }

            return <View wrap={false} style={styles.observation}>
                <Text>{observation.participants.length > 1 ? <Text style={{ fontWeight: 'bold' }}>{observation.participants.map(p => p.scout_name).join(' ')} </Text> : <Text />}{observation.content}</Text>
                <Text style={styles.observationMetadata}>{observation.block.name}, {new Date(observation.block.block_date).toLocaleDateString(document.documentElement.lang)}</Text>
            </View>
        }

        if (node.type === 'requirement') {
            const requirement = requirements.find(r => r.id === node.attrs.id)
            const status = statuses.find(s => s.id === node.attrs.status_id)

            if (!requirement || !status) {
                return <View />
            }

            return <View minPresenceAhead={30} style={{ position: 'relative' }}>
                <Text style={{ ...styles.requirementIcon, color: colors[status.color].color }}>{ icons[status.icon] }</Text>
                <Text style={styles.requirement}>{ ucfirst(requirement.content) }</Text>
            </View>
        }

        // Unknown node type
        return <View />
    }

    return transformToReactPdf(feedbackContents)
}

export default FeedbackContents
