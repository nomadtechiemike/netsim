<?php

declare (strict_types=1);
namespace Rector\PhpAttribute;

use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Php80\Contract\ValueObject\AnnotationToAttributeInterface;
use Rector\PhpAttribute\ValueObject\UseAliasMetadata;
/**
 * @see \Rector\Tests\PhpAttribute\UseAliasNameMatcherTest
 */
final class UseAliasNameMatcher
{
    /**
     * @param Use_[] $uses
     */
    public function match(array $uses, string $shortAnnotationName, AnnotationToAttributeInterface $annotationToAttribute) : ?UseAliasMetadata
    {
        $shortAnnotationName = \trim($shortAnnotationName, '@');
        foreach ($uses as $use) {
            foreach ($use->uses as $useUse) {
                // we need to use original use statement
                $originalUseUseNode = $useUse->getAttribute(AttributeKey::ORIGINAL_NODE);
                if (!$originalUseUseNode instanceof UseItem) {
                    continue;
                }
                if (!$originalUseUseNode->alias instanceof Identifier) {
                    continue;
                }
                $alias = $originalUseUseNode->alias->toString();
                if (\strncmp($shortAnnotationName, $alias, \strlen($alias)) !== 0) {
                    continue;
                }
                $fullyQualifiedAnnotationName = $originalUseUseNode->name->toString() . \ltrim($shortAnnotationName, $alias);
                if ($fullyQualifiedAnnotationName !== $annotationToAttribute->getTag()) {
                    continue;
                }
                $annotationParts = \explode('\\', $fullyQualifiedAnnotationName);
                $attributeParts = \explode('\\', $annotationToAttribute->getAttributeClass());
                // requirement for matching single part rename
                if (\count($annotationParts) !== \count($attributeParts)) {
                    continue;
                }
                // now we now we are matching correct contanct and old and new have the same number of parts
                $useImportPartCount = \substr_count($originalUseUseNode->name->toString(), '\\') + 1;
                $newAttributeImportPart = \array_slice($attributeParts, 0, $useImportPartCount);
                $newAttributeImport = \implode('\\', $newAttributeImportPart);
                $shortNamePartCount = \count($attributeParts) - $useImportPartCount;
                // +1, to remove the alias part
                $attributeParts = \array_slice($attributeParts, -$shortNamePartCount);
                $shortAttributeName = $alias . '\\' . \implode('\\', $attributeParts);
                return new UseAliasMetadata($shortAttributeName, $newAttributeImport, $useUse);
            }
        }
        return null;
    }
}
